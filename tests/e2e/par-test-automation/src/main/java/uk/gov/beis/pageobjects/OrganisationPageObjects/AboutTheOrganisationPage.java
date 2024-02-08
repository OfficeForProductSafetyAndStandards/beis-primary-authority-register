package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.SharedPageObjects.AddAddressPage;

public class AboutTheOrganisationPage extends BasePageObject {
	
	@FindBy(id = "edit-about-business")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public AboutTheOrganisationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public AddAddressPage goToAddressPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, AddAddressPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public BusinessDetailsPage goToBusinessDetailsPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
