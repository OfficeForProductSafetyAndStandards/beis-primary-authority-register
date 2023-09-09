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
	private WebElement authorityName;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public AuthorityTypePage enterAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityTypePage.class);
	}
	
	public AuthorityTypePage editAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
		
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityTypePage.class);
	}
}
