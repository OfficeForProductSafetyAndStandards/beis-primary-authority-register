package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class EmployeesPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	String noEmployees = "//select/option[contains(text(),'?')]";
	
	public EmployeesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public TradingPage selectNoEmployees(String number) {
		driver.findElement(By.xpath(noEmployees.replace("?", number))).click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
}
