package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedConditions;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class SICCodePage extends BasePageObject {

	public SICCodePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	String sic = "//li[contains(text(),'?')]";

	public BasePageObject selectSICCode(String code) {
		driver.findElement(By.xpath("//input[@type='text']")).sendKeys(code);
		WebElement autocompleteDropdown = wait.until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//ul[@role='listbox']")));
		autocompleteDropdown.findElement(By.xpath(sic.replace("?", code))).click();
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, EmployeesPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, BusinessConfirmationPage.class);
		}
	}
}
