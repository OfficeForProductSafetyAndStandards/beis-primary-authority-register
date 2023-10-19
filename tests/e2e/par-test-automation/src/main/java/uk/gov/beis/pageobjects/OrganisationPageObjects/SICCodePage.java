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

	public SICCodePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String sic = "//select/option[contains(text(),'?')]";

	public BasePageObject selectSICCode(String code) {
		driver.findElement(By.xpath(sic.replace("?", code))).click();
		
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, EmployeesPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, BusinessConfirmationPage.class);
		}
	}
	
	public PartnershipConfirmationPage editSICCode(String code) {
		driver.findElement(By.xpath(sic.replace("?", code))).click();
		
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
