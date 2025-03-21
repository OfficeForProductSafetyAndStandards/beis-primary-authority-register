package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class BusinessNamePage extends BasePageObject {
	
	@FindBy(id = "edit-name")
	private WebElement businessName;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public BusinessNamePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterBusinessName(String name) {
		businessName.clear();
		businessName.sendKeys(name);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
