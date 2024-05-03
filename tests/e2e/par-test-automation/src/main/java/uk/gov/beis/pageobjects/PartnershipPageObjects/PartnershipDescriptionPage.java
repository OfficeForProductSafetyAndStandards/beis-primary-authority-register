package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipDescriptionPage extends BasePageObject {
	
	@FindBy(id = "edit-about-partnership")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-about-business")
	private WebElement businessDescriptionBox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public PartnershipDescriptionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public void updateBusinessDescription(String description) {
		businessDescriptionBox.clear();
		businessDescriptionBox.sendKeys(description);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
