package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RemoveEnforcementConfirmationPage extends BasePageObject{

	public RemoveEnforcementConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Remove')]")
	WebElement removeBtn;

	public EnforcementSearchPage acceptTerms() {
		WebElement checkbox = driver.findElement(By.id("edit-confirm"));
		// Tick checkbox only if unchecked
		if (!checkbox.isSelected())
			checkbox.click();
		removeBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
}
