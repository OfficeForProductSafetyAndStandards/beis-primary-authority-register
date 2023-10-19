package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

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
	
	public void enterDescription(String description) { // May need a class specifically for updating
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public void updateBusinessDescription(String description) {
		businessDescriptionBox.clear();
		businessDescriptionBox.sendKeys(description);
	}
	
	public BusinessPage enterPartnershipDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		
		try {
			scrollToElement(driver.findElement(By.id("edit-next")));
			driver.findElement(By.id("edit-next")).click();
		} catch (Exception e) {
			scrollToElement(driver.findElement(By.id("edit-save")));
			driver.findElement(By.id("edit-save")).click();
		}

		return PageFactory.initElements(driver, BusinessPage.class);
	}
	
	public BusinessPage gotToBusinessNamePage() throws Throwable {
		continueBtn.click();

		return PageFactory.initElements(driver, BusinessPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public PartnershipConfirmationPage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
