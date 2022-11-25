package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class LegalEntityPage extends BasePageObject {

	public LegalEntityPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	String legalEntName = "//label[contains(text(),'Enter name of the legal entity')]/following-sibling::input";
	String legalEntType = "//select/option[contains(text(),'?')]";
	String regNo = "//label[contains(text(),'Provide the registration number')]/following-sibling::input";


	public PartnershipConfirmationPage createLegalEntity(String type) {
		DataStore.saveValue(UsableValues.ENTITY_NAME, RandomStringGenerator.getLegalEntityName(3));
		DataStore.saveValue(UsableValues.REGISTRATION_NO, RandomStringGenerator.getRandomNumericString(7));

		driver.findElement(By.xpath(legalEntName)).sendKeys(DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		driver.findElement(By.xpath(legalEntType.replace("?", type))).click();
		driver.findElement(By.xpath(regNo)).sendKeys(DataStore.getSavedValue(UsableValues.REGISTRATION_NO));

		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
