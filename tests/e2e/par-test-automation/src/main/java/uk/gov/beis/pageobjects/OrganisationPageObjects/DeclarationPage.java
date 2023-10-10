package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class DeclarationPage extends BasePageObject {

	public DeclarationPage() throws ClassNotFoundException, IOException {
		super();
	}

	private boolean advancedsearch = false;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public void setAdvancedSearch(boolean value) {
		this.advancedsearch = value;
	}

	public boolean getAdvancedSearch() {
		return this.advancedsearch;
	}

	public BusinessDetailsPage acceptTerms() {
		WebElement checkbox = getAdvancedSearch() ? driver.findElement(By.id("edit-confirm-authorisation-select"))
				: driver.findElement(By.id("edit-confirm"));
		if (!checkbox.isSelected())
			checkbox.click();
		continueBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
