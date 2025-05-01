package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PublicRegistrySearchPage extends BasePageObject {

	@FindBy(id = "edit-keywords")
	private WebElement searchInput;

	@FindBy(id = "edit-submit-primary-authority-register")
	private WebElement searchBtn;

	@FindBy(xpath = "//td[@class='views-field views-field-organisation-name']")
	private WebElement partnershipTableFirstElement;

	public PublicRegistrySearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void searchForPartnership(String partnership) {
        waitForElementToBeVisible(By.id("edit-keywords"), 2000);
		searchInput.sendKeys(partnership);
	}

	public void clickSearchButton() {
        waitForElementToBeVisible(By.id("edit-submit-primary-authority-register"), 2000);
        searchBtn.click();
        waitForPageLoad();
	}

	public Boolean partnershipContains(String name) {
        waitForElementToBeVisible(By.xpath("//td[@class='views-field views-field-organisation-name']"), 3000);
		return partnershipTableFirstElement.getText().contains(name);
	}
}
