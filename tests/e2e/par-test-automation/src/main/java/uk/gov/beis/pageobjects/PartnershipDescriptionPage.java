package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PartnershipDescriptionPage extends BasePageObject {

	public PartnershipDescriptionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	//@FindBy(id = "edit-about-business")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public BusinessPage enterPartnershipDescription(String description, boolean secondJourney) throws Throwable {
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
	
	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public PartnershipConfirmationPage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
