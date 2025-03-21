package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ONSCodePage extends BasePageObject {

	@FindBy(id = "edit-ons-code")
	private WebElement onsCode;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ONSCodePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterONSCode(String name) {
		onsCode.clear();
		onsCode.sendKeys(name);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
