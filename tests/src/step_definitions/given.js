import checkContainsAnyText from '../support/check/checkContainsAnyText';
import checkContainsText from '../support/check/checkContainsText';
import checkCookieContent from '../support/check/checkCookieContent';
import checkCookieExists from '../support/check/checkCookieExists';
import checkDimension from '../support/check/checkDimension';
import checkElementExists from '../support/check/checkElementExists';
import checkEqualsText from '../support/check/checkEqualsText';
import checkModal from '../support/check/checkModal';
import checkOffset from '../support/check/checkOffset';
import checkProperty from '../support/check/checkProperty';
import checkSelected from '../support/check/checkSelected';
import checkTitle from '../support/check/checkTitle';
import checkUrl from '../support/check/checkURL';
import closeAllButFirstTab from '../support/action/closeAllButFirstTab';
import compareText from '../support/check/compareText';
import isEnabled from '../support/check/isEnabled';
import isVisible from '../support/check/isVisible';
import openWebsite from '../support/action/openWebsite';
import checkResponseCode from '../support/action/checkResponseCode';
import resizeScreenSize from '../support/action/resizeScreenSize';
import loginAsPARUser from '../support/action/loginAsPARUser';
import resetTheTestData from '../support/action/resetTheTestData';
import selectNextPartnershipAwaitingReview from '../support/action/selectNextPartnershipAwaitingReview';
import selectNextBusinessAwaitingReview from '../support/action/selectNextPartnershipAwaitingReview';
import selectNextHelpdeskPartnershipAwaitingReview from '../support/action/selectNextHelpdeskPartnershipAwaitingReview';
import selectNextHelpdeskBusinessAwaitingReview from '../support/action/selectNextHelpdeskBusinessAwaitingReview';
import selectMyNextCoordinatedPartnership from '../support/action/selectMyNextCoordinatedPartnership';
import relevantSearchResultsCheck from '../support/action/relevantSearchResultsCheck';
import partnershipSearchResultsCheck from '../support/action/partnershipSearchResultsCheck';
import relevantSearchResultsCheckHelpdesk from '../support/action/relevantSearchResultsCheckHelpdesk';

module.exports = function given() {
    this.Given(
        /^I open the (url|site) "([^"]*)?"$/,
        openWebsite
    );

    this.Given(
        /^I check the homepage response code is 200$/,
        checkResponseCode
    );

    this.Given(
        /^the element "([^"]*)?" is( not)* visible$/,
        isVisible
    );

    this.Given(
        /^the element "([^"]*)?" is( not)* enabled$/,
        isEnabled
    );

    this.Given(
        /^the element "([^"]*)?" is( not)* selected$/,
        checkSelected
    );

    this.Given(
        /^the checkbox "([^"]*)?" is( not)* checked$/,
        checkSelected
    );

    this.Given(
        /^there is (an|no) element "([^"]*)?" on the page$/,
        checkElementExists
    );

    this.Given(
        /^the title is( not)* "([^"]*)?"$/,
        checkTitle
    );

    this.Given(
        /^the element "([^"]*)?" contains( not)* the same text as element "([^"]*)?"$/,
        compareText
    );

    this.Given(
        /^the (element|inputfield) "([^"]*)?"( not)* matches the text "([^"]*)?"$/,
        checkEqualsText
    );

    this.Given(
        /^I am logged in as "([^"]*)?"$/,
        loginAsPARUser
    );

    this.Given(
        /^I select next partnership awaiting review$/,
        selectNextPartnershipAwaitingReview
    );

    this.Given(
        /^I select next business awaiting review$/,
        selectNextBusinessAwaitingReview
    );

    this.Given(
        /^I select next helpdesk partnership awaiting review$/,
        selectNextHelpdeskPartnershipAwaitingReview
    );

    this.Given(
        /^I select next helpdesk business awaiting review$/,
        selectNextHelpdeskBusinessAwaitingReview
    );

    this.Given(
        /^relevant partnerships search results returned for search term "([^"]*)?"$/,
        relevantSearchResultsCheck
    );

    this.Given(
        /^relevant search results returned for partnership search term "([^"]*)?"$/,
        partnershipSearchResultsCheck
    );


    this.Given(
        /^relevant helpdesk results returned for search term "([^"]*)?"$/,
        relevantSearchResultsCheckHelpdesk
    );
    this.Given(
        /^I select my next coordinated partnership awaiting review$/,
        selectMyNextCoordinatedPartnership
    );
    

    this.Given(
        /^I reset the test data$/,
        resetTheTestData
    );

    this.Given(
        /^the (element|inputfield) "([^"]*)?"( not)* contains the text "([^"]*)?"$/,
        checkContainsText
    );

    this.Given(
        /^the (element|inputfield) "([^"]*)?"( not)* contains any text$/,
        checkContainsAnyText
    );

    this.Given(
        /^the page url is( not)* "([^"]*)?"$/,
        checkUrl
    );

    this.Given(
        /^the( css)* attribute "([^"]*)?" from element "([^"]*)?" is( not)* "([^"]*)?"$/,
        checkProperty
    );

    this.Given(
        /^the cookie "([^"]*)?" contains( not)* the value "([^"]*)?"$/,
        checkCookieContent
    );

    this.Given(
        /^the cookie "([^"]*)?" does( not)* exist$/,
        checkCookieExists
    );

    this.Given(
        /^the element "([^"]*)?" is( not)* ([\d]+)px (broad|tall)$/,
        checkDimension
    );

    this.Given(
        /^the element "([^"]*)?" is( not)* positioned at ([\d]+)px on the (x|y) axis$/,
        checkOffset
    );

    this.Given(
        /^I have a screen that is ([\d]+) by ([\d]+) pixels$/,
        resizeScreenSize
    );

    this.Given(
        /^I have closed all but the first (window|tab)$/,
        closeAllButFirstTab
    );

    this.Given(
        /^a (alertbox|confirmbox|prompt) is( not)* opened$/,
        checkModal
    );
};
