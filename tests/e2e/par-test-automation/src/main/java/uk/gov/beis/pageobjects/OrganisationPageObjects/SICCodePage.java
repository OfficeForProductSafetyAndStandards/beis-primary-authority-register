package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class SICCodePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String sicCodeLocator = "//select/option[contains(text(),'?')]";

	public SICCodePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectPrimarySICCode(String code) {
		driver.findElement(By.xpath(sicCodeLocator.replace("?", code))).click();
	}
	
	public NumberOfEmployeesPage selectSICCode(String code) {
		driver.findElement(By.xpath(sicCodeLocator.replace("?", code))).click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, NumberOfEmployeesPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public PartnershipConfirmationPage editSICCode(String code) {
		driver.findElement(By.xpath(sicCodeLocator.replace("?", code))).click();
		
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public BusinessDetailsPage goToBusinessDetailsPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
