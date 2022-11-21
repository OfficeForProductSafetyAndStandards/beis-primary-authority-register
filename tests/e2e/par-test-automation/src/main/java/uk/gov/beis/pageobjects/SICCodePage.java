package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class SICCodePage extends BasePageObject {

	public SICCodePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	String sic = "//select/option[contains(text(),'?')]";

	public EmployeesPage selectSICCode(String code) {
		driver.findElement(By.xpath(sic.replace("?", code))).click();
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, EmployeesPage.class);
	}
}
