package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ProposedEnforcementPage extends BasePageObject{
	
	public ProposedEnforcementPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	@FindBy(xpath = "//label[contains(text(),'Allow')]")
	WebElement allowBtn;


	public ProposedEnforcementPage selectAllow() {
		allowBtn.click();
		return PageFactory.initElements(driver, ProposedEnforcementPage.class);
	}

	public EnforcementReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementReviewPage.class);
	}
	
}
