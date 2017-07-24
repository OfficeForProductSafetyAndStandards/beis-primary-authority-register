(function ($, Drupal, drupalSettings, CKEDITOR) {

    'use strict';

    function noBlockLeft( bqBlock ) {
        for ( var i = 0, length = bqBlock.getChildCount(), child; i < length && ( child = bqBlock.getChild( i ) ); i++ ) {
            if ( child.type === CKEDITOR.NODE_ELEMENT && child.isBlockBoundary() )
                return false;
        }
        return true;
    }

    function processItems(editor, par_class, command) {

        var state = editor.getCommand( command ).state,
            selection = editor.getSelection(),
            range = selection && selection.getRanges()[ 0 ];

        if ( !range )
            return;

        var bookmarks = selection.createBookmarks();

        // If the bookmark nodes are in the beginning then move them to the nearest block element.
        if ( CKEDITOR.env.ie ) {
            var bookmarkStart = bookmarks[ 0 ].startNode,
                bookmarkEnd = bookmarks[ 0 ].endNode,
                cursor;

            if ( bookmarkStart && bookmarkStart.getParent().getName() === 'div' ) {
                cursor = bookmarkStart;
                while ( ( cursor = cursor.getNext() ) ) {
                    if ( cursor.type === CKEDITOR.NODE_ELEMENT && cursor.isBlockBoundary() ) {
                        bookmarkStart.move( cursor, true );
                        break;
                    }
                }
            }

            if ( bookmarkEnd && bookmarkEnd.getParent().getName() === 'div' ) {
                cursor = bookmarkEnd;
                while ( ( cursor = cursor.getPrevious() ) ) {
                    if ( cursor.type === CKEDITOR.NODE_ELEMENT && cursor.isBlockBoundary() ) {
                        bookmarkEnd.move( cursor );
                        break;
                    }
                }
            }
        }

        var iterator = range.createIterator(),
            block;
        iterator.enlargeBr = editor.config.enterMode !== CKEDITOR.ENTER_BR;

        if ( state === CKEDITOR.TRISTATE_OFF ) {
            var paragraphs = [];
            while ( ( block = iterator.getNextParagraph() ) )
                paragraphs.push( block );

            // If no paragraphs, create one from the current selection position.
            if ( paragraphs.length < 1 ) {
                var para = editor.document.createElement( editor.config.enterMode === CKEDITOR.ENTER_P ? 'p' : 'div' ),
                    firstBookmark = bookmarks.shift();
                range.insertNode( para );
                para.append( new CKEDITOR.dom.text( '\ufeff', editor.document ) );
                range.moveToBookmark( firstBookmark );
                range.selectNodeContents( para );
                range.collapse(true);
                firstBookmark = range.createBookmark();
                paragraphs.push( para );
                bookmarks.unshift( firstBookmark );
            }

            // Make sure all paragraphs have the same parent.
            var commonParent = paragraphs[ 0 ].getParent(),
                tmp = [];
            for ( var i = 0; i < paragraphs.length; i++ ) {
                block = paragraphs[ i ];
                commonParent = commonParent.getCommonAncestor( block.getParent() );
            }

            // The common parent must not be the following tags: table, tbody, tr, ol, ul.
            var denyTags = { table: 1, tbody: 1, tr: 1, ol: 1, ul: 1 };
            while ( denyTags[ commonParent.getName() ] )
                commonParent = commonParent.getParent();

            // Reconstruct the block list to be processed such that all resulting blocks
            // satisfy parentNode.equals( commonParent ).
            var lastBlock = null;
            while ( paragraphs.length > 0 ) {
                block = paragraphs.shift();
                while ( !block.getParent().equals( commonParent ) )
                    block = block.getParent();
                if ( !block.equals( lastBlock ) )
                    tmp.push( block );
                lastBlock = block;
            }

            // If any of the selected blocks is a div, remove it to prevent
            // nested divs.
            while ( tmp.length > 0 ) {
                block = tmp.shift();
                if ( block.getName() === 'div' ) {
                    var docFrag = new CKEDITOR.dom.documentFragment( editor.document );
                    while ( block.getFirst() ) {
                        docFrag.append( block.getFirst().remove() );
                        paragraphs.push( docFrag.getLast() );
                    }

                    docFrag.replace( block );
                } else {
                    paragraphs.push( block );
                }
            }

            // Now we have all the blocks to be included in a new div node.
            var bqBlock = editor.document.createElement( 'div' );
            bqBlock.$.className = par_class;
            bqBlock.insertBefore(paragraphs[0]);
            while ( paragraphs.length > 0 ) {
                block = paragraphs.shift();
                bqBlock.append( block );
            }
        }
        else if ( state === CKEDITOR.TRISTATE_ON ) {
            var moveOutNodes = [],
                database = {};

            while ( ( block = iterator.getNextParagraph() ) ) {
                var bqParent = null,
                    bqChild = null;
                while ( block.getParent() ) {
                    if ( block.getParent().getName() === 'div' ) {
                        bqParent = block.getParent();
                        bqChild = block;
                        break;
                    }
                    block = block.getParent();
                }

                // Remember the blocks that were recorded down in the moveOutNodes array
                // to prevent duplicates.
                if ( bqParent && bqChild && !bqChild.getCustomData( 'div_moveout' ) ) {
                    moveOutNodes.push( bqChild );
                    CKEDITOR.dom.element.setMarker( database, bqChild, 'div_moveout', true );
                }
            }

            CKEDITOR.dom.element.clearAllMarkers( database );

            var movedNodes = [],
                processedDivBlocks = [];

            database = {};
            while ( moveOutNodes.length > 0 ) {
                var node = moveOutNodes.shift();
                bqBlock = node.getParent();

                // If the node is located at the beginning or the end, just take it out
                // without splitting. Otherwise, split the div node and move the
                // paragraph in between the two div nodes.
                if ( !node.getPrevious() )
                    node.remove().insertBefore( bqBlock );
                else if ( !node.getNext() )
                    node.remove().insertAfter( bqBlock );
                else {
                    node.breakParent( node.getParent() );
                    processedDivBlocks.push( node.getNext() );
                }

                // Remember the div node so we can clear it later (if it becomes empty).
                if ( !bqBlock.getCustomData( 'div_processed' ) ) {
                    processedDivBlocks.push( bqBlock );
                    CKEDITOR.dom.element.setMarker( database, bqBlock, 'div_processed', true );
                }

                movedNodes.push( node );
            }

            CKEDITOR.dom.element.clearAllMarkers( database );

            // Clear div nodes that have become empty.
            for ( i = processedDivBlocks.length - 1; i >= 0; i-- ) {
                bqBlock = processedDivBlocks[ i ];
                if ( noBlockLeft( bqBlock ) )
                    bqBlock.remove();
            }

            if ( editor.config.enterMode === CKEDITOR.ENTER_BR ) {
                var firstTime = true;
                while ( movedNodes.length ) {
                    node = movedNodes.shift();

                    if ( node.getName() === 'div' ) {
                        docFrag = new CKEDITOR.dom.documentFragment( editor.document );
                        var needBeginBr = firstTime && node.getPrevious() && !( node.getPrevious().type === CKEDITOR.NODE_ELEMENT && node.getPrevious().isBlockBoundary() );
                        if ( needBeginBr )
                            docFrag.append( editor.document.createElement( 'br' ) );

                        var needEndBr = node.getNext() && !( node.getNext().type === CKEDITOR.NODE_ELEMENT && node.getNext().isBlockBoundary() );
                        while ( node.getFirst() )
                            node.getFirst().remove().appendTo( docFrag );

                        if ( needEndBr )
                            docFrag.append( editor.document.createElement( 'br' ) );

                        docFrag.replace( node );
                        firstTime = false;
                    }
                }
            }
        }

        selection.selectBookmarks( bookmarks );
        editor.focus();
    }

    // Create the plugin. The name needs to match the class name.
    CKEDITOR.plugins.add( 'parstylebuttons', {
        icons: 'infonotice,contact,helpnotice',
        hidpi: true,
        init: function( editor ) {
            if ( editor.blockless ) {
                return;
            }

            editor.addCommand('parcontact', {
                contextSensitive: 1,
                exec: function (editor) {
                    processItems(editor, 'contact', 'parcontact');
                },
                refresh: function (editor, path) {
                    var element = path.lastElement && path.lastElement.getAscendant('div', true);

                    if (element && element.getName() === 'div' && element.getAttribute('class') && element.getAttribute('class') === 'contact') {
                        this.setState(CKEDITOR.TRISTATE_ON);
                    }
                    else {
                        this.setState(CKEDITOR.TRISTATE_OFF);
                    }
                }
            });

            editor.addCommand('parinfonotice', {
                contextSensitive: 1,
                exec: function (editor) {
                    processItems(editor, 'info-notice', 'parinfonotice');
                },
                refresh: function (editor, path) {
                    var element = path.lastElement && path.lastElement.getAscendant('div', true);

                    if (element && element.getName() === 'div' && element.getAttribute('class') && element.getAttribute('class') === 'info-notice') {
                        this.setState(CKEDITOR.TRISTATE_ON);
                    }
                    else {
                        this.setState(CKEDITOR.TRISTATE_OFF);
                    }
                }
            });

            editor.addCommand('parhelpnotice', {
                contextSensitive: 1,
                exec: function (editor) {
                    processItems(editor, 'help-notice', 'parhelpnotice');
                },
                refresh: function (editor, path) {
                    var element = path.lastElement && path.lastElement.getAscendant('div', true);

                    if (element && element.getName() === 'div' && element.getAttribute('class') && element.getAttribute('class') === 'help-notice') {
                        this.setState(CKEDITOR.TRISTATE_ON);
                    }
                    else {
                        this.setState(CKEDITOR.TRISTATE_OFF);
                    }
                }
            });

            // Add the buttons.
            if (editor.ui.addButton) {
                editor.ui.addButton('Contact', {
                    label: Drupal.t('Contact'),
                    command: 'parcontact'
                });
            }

            if (editor.ui.addButton) {
                editor.ui.addButton('InfoNotice', {
                    label: Drupal.t('Information Notice'),
                    command: 'parinfonotice'
                });
            }

            if (editor.ui.addButton) {
                editor.ui.addButton('HelpNotice', {
                    label: Drupal.t('Help Notice'),
                    command: 'parhelpnotice'
                });
            }

        }
    } );
})(jQuery, Drupal, drupalSettings, CKEDITOR);
