import { defineSupportCode } from 'cucumber';
import clearInputField from '../support/action/clearInputField';
import setCheckbox from '../support/action/setCheckbox';
import clickElement from '../support/action/clickElement';
import clickChildElement from '../support/action/clickElement';
import closeLastOpenedWindow from '../support/action/closeLastOpenedWindow';
import deleteCookie from '../support/action/deleteCookie';
import dragElement from '../support/action/dragElement';
import focusLastOpenedWindow from '../support/action/focusLastOpenedWindow';
import handleModal from '../support/action/handleModal';
import moveToElement from '../support/action/moveToElement';
import pause from '../support/action/pause';
import pressButton from '../support/action/pressButton';
import scroll from '../support/action/scroll';
import selectOption from '../support/action/selectOption';
import selectOptionByIndex from '../support/action/selectOptionByIndex';
import setCookie from '../support/action/setCookie';
import setInputField from '../support/action/setInputField';
import enterInRandomText from '../support/action/enterInRandomText';
import setPromptText from '../support/action/setPromptText';
import tota11y from '../support/action/tota11y';
import submitForm from '../support/action/submitForm';
import uploadTheFile from '../support/action/uploadTheFile.js';
import extractInvitationUrl from '../support/action/extractInvitationUrl.js';
import completeInvitationEmail from '../support/action/completeInvitationEmail.js';
import selectAnAuthorityForPartnership from '../support/action/selectAnAuthorityForPartnership';


defineSupportCode(({ When }) => {
    When(
        /^I (click|doubleclick) on the (link|button|radio|checkbox|business|element) "([^"]*)?"$/,
        clickElement
    );

    When(
        /^I (check|uncheck) the checkbox "([^"]*)?"$/,
        setCheckbox
    );

    When(
        /^I extract the invitation url$/,
        extractInvitationUrl
    );

    When(
        /^I complete the invitation process$/,
        completeInvitationEmail
    );

    When(
        /^I run tota11y against the current page$/,
        tota11y
    );

    When(
        /^I click on authority selection if available$/,
        selectAnAuthorityForPartnership
    );

    When(
        /^I click on the link "([^"]*)?" in the page area "([^"]*)?"$/,
        clickChildElement
    );

    When(
        /^I (add|set) "([^"]*)?" to the inputfield "([^"]*)?"$/,
        setInputField
    );

    When(
        /^I add "([^"]*)?" random chars of text to field "([^"]*)?"$/,
        enterInRandomText
    );

    When(
        /^I clear the inputfield "([^"]*)?"$/,
        clearInputField
    );

    When(
        /^I upload the file "([^"]*)?" to field "([^"]*)?"$/,
        uploadTheFile
    );

    When(
        /^I drag element "([^"]*)?" to element "([^"]*)?"$/,
        dragElement
    );

    When(
        /^I submit the form "([^"]*)?"$/,
        submitForm
    );

    When(
        /^I pause for (\d+)ms$/,
        pause
    );

    When(
        /^I set a cookie "([^"]*)?" with the content "([^"]*)?"$/,
        setCookie
    );

    When(
        /^I delete the cookie "([^"]*)?"$/,
        deleteCookie
    );

    When(
        /^I press "([^"]*)?"$/,
        pressButton
    );

    When(
        /^I (accept|dismiss) the (alertbox|confirmbox|prompt)$/,
        handleModal
    );

    When(
        /^I enter "([^"]*)?" into the prompt$/,
        setPromptText
    );

    When(
        /^I scroll to element "([^"]*)?"$/,
        scroll
    );

    When(
        /^I close the last opened (window|tab)$/,
        closeLastOpenedWindow
    );

    When(
        /^I focus the last opened (window|tab)$/,
        focusLastOpenedWindow
    );

    When(
        /^I select the (\d+)(st|nd|rd|th) option for element "([^"]*)?"$/,
        selectOptionByIndex
    );

    When(
        /^I select the option with the (name|value|text) "([^"]*)?" for element "([^"]*)?"$/,
        selectOption
    );

    When(
        /^I move to element "([^"]*)?"( with an offset of (\d+),(\d+))*$/,
        moveToElement
    );
});
