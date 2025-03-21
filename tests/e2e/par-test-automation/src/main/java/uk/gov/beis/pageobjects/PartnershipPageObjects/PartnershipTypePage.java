package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipTypePage extends BasePageObject {

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String locator = "//label[contains(text(),'?')]";
	
	public PartnershipTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectPartnershipType(String type) {
		driver.findElement(By.xpath(locator.replace("?", type))).click();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
