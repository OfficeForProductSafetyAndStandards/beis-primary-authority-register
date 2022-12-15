package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityNamePage extends BasePageObject {
	
	public AuthorityNamePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-name")
	WebElement authorityName;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public AuthorityTypePage enterAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
		continueBtn.click();

		return PageFactory.initElements(driver, AuthorityTypePage.class);
	}

}
