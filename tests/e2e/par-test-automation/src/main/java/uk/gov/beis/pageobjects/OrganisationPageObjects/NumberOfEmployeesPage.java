package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;

public class NumberOfEmployeesPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	String noEmployees = "//select/option[contains(text(),'?')]";
	
	public NumberOfEmployeesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectNumberOfEmployees(String number) {
		driver.findElement(By.xpath(noEmployees.replace("?", number))).click();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public TradingPage selectNoEmployees(String number) {
		driver.findElement(By.xpath(noEmployees.replace("?", number))).click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
}
