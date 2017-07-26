@ci
Feature:  As a Primary Authority Officer,	
I need to be able to agree to the Primary Authority Terms and Conditions (for each Partnership),	
so that my Partnerships remain valid after 1st October.

    Background:
        Given I open the url "/user/login"
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details"
        And I scroll to element "#edit-confirmation"
        Then I expect that element "#edit-confirmation" is visible


git add package.json
git add src/features/PAR11-List-Partnership-Details-List-Regulatory-Areas.feature
git add src/features/PAR175-List-of-Business-for-a-Primary-Authority-Create-view.feature
git add src/features/PAR179-Capture-Partnership-Entity-Section-States.feature
git add src/features/PAR184-List-Partnership-Details.feature
git add src/features/PAR186-Edit-About-The-Partnership-Load-Form-Data.feature
git add src/features/PAR187-Edit-About-The-Partnership-Save-Form-Data.feature
git add src/features/PAR188-Edit-Main-PA-Contact-Load-Form-Data.feature
git add src/features/PAR248-PA-Officer-Confirming-TandCs.feature
git add src/features/PAR64-Edit-About-The-Partnership-Create-Form-And-Add-To-Flow.feature
git add src/features/PAR66-Edit-Second-PA-Contact-Create-Form.feature
git add src/features/PAR188-Edit-Main-PA-Contact-Load-Form-Data.1.feature
git add src/features/pending/