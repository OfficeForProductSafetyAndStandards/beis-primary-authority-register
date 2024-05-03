package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AuthorityNamePage extends BasePageObject {

	@FindBy(id = "edit-name")
	private WebElement authorityName;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public AuthorityNamePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
