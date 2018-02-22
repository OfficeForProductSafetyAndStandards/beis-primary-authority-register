import { defineSupportCode } from 'cucumber';

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
import storeEOData from '../support/action/storeEOData';
import checkResponseCode from '../support/action/checkResponseCode';
import resizeScreenSize from '../support/action/resizeScreenSize';
import loginAsStoredUser from '../support/action/loginAsStoredUser';
import selectNextPartnershipAwaitingReview from '../support/action/selectNextPartnershipAwaitingReview';
import selectNextBusinessAwaitingReview from '../support/action/selectNextPartnershipAwaitingReview';
import selectNextHelpdeskPartnershipAwaitingReview from '../support/action/selectNextHelpdeskPartnershipAwaitingReview';
import selectNextHelpdeskBusinessAwaitingReview from '../support/action/selectNextHelpdeskBusinessAwaitingReview';
import selectMyNextCoordinatedPartnership from '../support/action/selectMyNextCoordinatedPartnership';
import clickNewPartnership from '../support/action/clickNewPartnership';
import openMembersAddPage from '../support/action/openMembersAddPage';


defineSupportCode(({ Given }) => {
    Given(
        /^I open the (url|site) "([^"]*)?"$/,
        openWebsite
    );

    Given(
        /^I open the add members page$/,
        openMembersAddPage
    );
    Given(
        /^I click new partnership if presented with choices$/,
        clickNewPartnership
    );

    Given(
        /^I store all EO data to use in later step$/,
        storeEOData
    );

    Given(
        /^I check the homepage response code is 200$/,
        checkResponseCode
    );

    Given(
        /^the element "([^"]*)?" is( not)* visible$/,
        isVisible
    );

    Given(
        /^the element "([^"]*)?" is( not)* enabled$/,
        isEnabled
    );

    Given(
        /^the element "([^"]*)?" is( not)* selected$/,
        checkSelected
    );

    Given(
        /^the checkbox "([^"]*)?" is( not)* checked$/,
        checkSelected
    );

    Given(
        /^there is (an|no) element "([^"]*)?" on the page$/,
        checkElementExists
    );

    Given(
        /^the title is( not)* "([^"]*)?"$/,
        checkTitle
    );

    Given(
        /^the element "([^"]*)?" contains( not)* the same text as element "([^"]*)?"$/,
        compareText
    );

    Given(
        /^the (element|inputfield) "([^"]*)?"( not)* matches the text "([^"]*)?"$/,
        checkEqualsText
    );

    Given(
        /^I am logged in as stored user$/,
        loginAsStoredUser
    );

    Given(
        /^the (element|inputfield) "([^"]*)?"( not)* contains the text "([^"]*)?"$/,
        checkContainsText
    );

    Given(
        /^the (element|inputfield) "([^"]*)?"( not)* contains any text$/,
        checkContainsAnyText
    );

    Given(
        /^the page url is( not)* "([^"]*)?"$/,
        checkUrl
    );

    Given(
        /^the( css)* attribute "([^"]*)?" from element "([^"]*)?" is( not)* "([^"]*)?"$/,
        checkProperty
    );

    Given(
        /^the cookie "([^"]*)?" contains( not)* the value "([^"]*)?"$/,
        checkCookieContent
    );

    Given(
        /^the cookie "([^"]*)?" does( not)* exist$/,
        checkCookieExists
    );

    Given(
        /^the element "([^"]*)?" is( not)* ([\d]+)px (broad|tall)$/,
        checkDimension
    );

    Given(
        /^the element "([^"]*)?" is( not)* positioned at ([\d]+)px on the (x|y) axis$/,
        checkOffset
    );

    Given(
        /^I have a screen that is ([\d]+) by ([\d]+) pixels$/,
        resizeScreenSize
    );

    Given(
        /^I have closed all but the first (window|tab)$/,
        closeAllButFirstTab
    );

    Given(
        /^a (alertbox|confirmbox|prompt) is( not)* opened$/,
        checkModal
    );
});
