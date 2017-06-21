class Dashing.Travis extends Dashing.Widget
  ready: ->
    @_checkStatus(@items[0].state)

  onData: (data) ->
    debugger
    @_checkStatus(data.items[0].state)

  _checkStatus: (status) ->
    $(@node).removeClass('errored failed passed started')
    $(@node).addClass(status)