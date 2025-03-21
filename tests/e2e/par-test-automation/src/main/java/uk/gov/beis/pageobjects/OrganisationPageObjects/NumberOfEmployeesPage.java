package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

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
}
