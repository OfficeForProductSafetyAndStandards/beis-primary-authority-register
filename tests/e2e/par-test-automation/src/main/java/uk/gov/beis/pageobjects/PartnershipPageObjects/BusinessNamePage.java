package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.AddAddressPage;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.OrganisationPageObjects.BusinessDetailsPage;

public class BusinessNamePage extends BasePageObject {
	
	@FindBy(id = "edit-name")
	private WebElement businessName;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public BusinessNamePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterBusinessName(String name) {
		businessName.clear();
		businessName.sendKeys(name);
	}
	
	public AddAddressPage goToAddressPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, AddAddressPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public BusinessDetailsPage goToBusinessConfirmationPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
