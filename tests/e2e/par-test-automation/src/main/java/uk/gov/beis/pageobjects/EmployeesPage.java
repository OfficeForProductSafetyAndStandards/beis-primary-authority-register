package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EmployeesPage extends BasePageObject {

	public EmployeesPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	String noEmployees = "//select/option[contains(text(),'?')]";

	public TradingPage selectNoEmployees(String code) {
		driver.findElement(By.xpath(noEmployees.replace("?", code))).click();
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
}
