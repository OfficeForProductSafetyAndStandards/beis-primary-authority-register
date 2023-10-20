package uk.gov.beis.pageobjects.DuplicateClasses;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.EnforcementSearchPage;

public class RemoveEnforcementConfirmationPage extends BasePageObject{
	
	@FindBy(xpath = "//input[contains(@value,'Remove')]")
	private WebElement removeBtn;
	
	public RemoveEnforcementConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public EnforcementSearchPage acceptTerms() {
		WebElement checkbox = driver.findElement(By.id("edit-confirm"));
		// Tick checkbox only if unchecked
		if (!checkbox.isSelected())
			checkbox.click();
		removeBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
}
