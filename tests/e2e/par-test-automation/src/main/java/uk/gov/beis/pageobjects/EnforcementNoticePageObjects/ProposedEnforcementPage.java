package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ProposedEnforcementPage extends BasePageObject{
	
	@FindBy(xpath = "//label[contains(text(),'Allow')]")
	private WebElement allowRadial;
	
	@FindBy(xpath = "//label[contains(text(),'Block')]")
	private WebElement blockRadial;
	
	@FindBy(id = "edit-par-component-enforcement-action-review-0-primary-authority-notes")
	private WebElement blockReasonTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public ProposedEnforcementPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectAllow() {
		allowRadial.click();
	}
	
	public void selectBlock() {
		blockRadial.click();
	}
	
	public void enterReasonForBlockingEnforcement(String reason) {
		blockReasonTextArea.clear();
		blockReasonTextArea.sendKeys(reason);
	}

	public EnforcementReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementReviewPage.class);
	}
}
