package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.AddAddressPage;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.BusinessConfirmationPage;

public class BusinessPage extends BasePageObject {
	
	@FindBy(id = "edit-name")
	private WebElement businessName;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public BusinessPage() throws ClassNotFoundException, IOException {
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
	
	public BusinessConfirmationPage goToBusinessConfirmationPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessConfirmationPage.class);
	}
}
