package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class SICCodePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String sicCodeLocator = "//select/option[contains(text(),'?')]";

	public SICCodePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectSICCode(String code) {
		driver.findElement(By.xpath(sicCodeLocator.replace("?", code))).click();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
