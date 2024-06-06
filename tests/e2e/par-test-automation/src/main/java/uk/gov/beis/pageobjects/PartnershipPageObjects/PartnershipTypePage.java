package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipTypePage extends BasePageObject {

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String locator = "//label[contains(text(),'?')]";
	
	public PartnershipTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public PartnershipTermsPage selectPartnershipType(String type) {
		driver.findElement(By.xpath(locator.replace("?", type))).click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipTermsPage.class);
	}

}
