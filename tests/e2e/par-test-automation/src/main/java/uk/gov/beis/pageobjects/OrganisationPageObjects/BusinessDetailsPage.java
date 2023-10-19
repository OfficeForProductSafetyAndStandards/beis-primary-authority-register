package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.AddAddressPage;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.BusinessConfirmationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;

public class BusinessDetailsPage extends BasePageObject {
	
	@FindBy(id = "edit-about-business")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public BusinessDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public AddAddressPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, AddAddressPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public BusinessConfirmationPage goToBusinessReviewPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessConfirmationPage.class);
	}
}
