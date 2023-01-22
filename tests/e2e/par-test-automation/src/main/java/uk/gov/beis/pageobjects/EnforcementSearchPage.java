package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class EnforcementSearchPage extends BasePageObject {

	public EnforcementSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-combine")
	WebElement searchInput;

	String status = "//td[contains(text(),'?')]/preceding-sibling::td[1]";

	@FindBy(xpath = "//input[contains(@value,'Apply')]")
	WebElement searchBtn;

	public AuthorityPage searchPartnerships() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchBtn.click();
		return PageFactory.initElements(driver, AuthorityPage.class);
	}

	public ProposedEnforcementPage selectEnforcement() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE))).click();
		return PageFactory.initElements(driver, ProposedEnforcementPage.class);
	}

	public String getStatus() {
		return driver.findElement(By.xpath(status.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))))
				.getText();

	}
}
