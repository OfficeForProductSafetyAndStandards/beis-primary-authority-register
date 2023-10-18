package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.BusinessConfirmationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class SICCodePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String sic = "//select/option[contains(text(),'?')]";

	public SICCodePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public EmployeesPage selectSICCode(String code) {
		driver.findElement(By.xpath(sic.replace("?", code))).click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, EmployeesPage.class);
	}
	
	public PartnershipConfirmationPage editSICCode(String code) {
		driver.findElement(By.xpath(sic.replace("?", code))).click();
		
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public BusinessConfirmationPage changeSICCode(String code) {
		driver.findElement(By.xpath(sic.replace("?", code))).click();
		
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessConfirmationPage.class);
	}
}
