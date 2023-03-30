package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class DeviationApprovalPage extends BasePageObject{
	
	public DeviationApprovalPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	@FindBy(xpath = "//label[contains(text(),'Allow')]")
	WebElement allowBtn;


	public DeviationApprovalPage selectAllow() {
		allowBtn.click();
		return PageFactory.initElements(driver, DeviationApprovalPage.class);
	}

	public DeviationReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, DeviationReviewPage.class);
	}
	
}
