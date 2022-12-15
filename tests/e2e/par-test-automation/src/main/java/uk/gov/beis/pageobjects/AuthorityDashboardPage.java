package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityDashboardPage extends BasePageObject {
	
	public AuthorityDashboardPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Add an authority")
	WebElement addAuthorityBtn;
	
	public AuthorityNamePage selectAddAuthority() {
		addAuthorityBtn.click();
		return PageFactory.initElements(driver, AuthorityNamePage.class);
	}
}
