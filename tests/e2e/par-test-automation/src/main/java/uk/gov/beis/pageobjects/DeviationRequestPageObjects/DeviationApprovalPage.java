package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class DeviationApprovalPage extends BasePageObject{

	@FindBy(id = "edit-primary-authority-status-approved")
	private WebElement allowRadial;
	
	@FindBy(id = "edit-primary-authority-status-blocked")
	private WebElement blockRadial;
	
	@FindBy(id = "edit-primary-authority-notes")
	private WebElement blockReasonTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	public DeviationApprovalPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectAllow() {
		allowRadial.click();
	}
	
	public void selectBlock() {
		blockRadial.click();
	}
	
	public void enterReasonForBlocking(String reason) {
		blockReasonTextArea.clear();
		blockReasonTextArea.sendKeys(reason);
	}
	
	public void clearAllFields() {
		blockReasonTextArea.clear();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
