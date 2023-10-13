package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class DeviationApprovalPage extends BasePageObject{

	@FindBy(xpath = "//label[contains(text(),'Allow')]")
	private WebElement allowRadial;
	
	@FindBy(xpath = "//label[contains(text(),'Block')]")
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

	public DeviationReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, DeviationReviewPage.class);
	}
}
