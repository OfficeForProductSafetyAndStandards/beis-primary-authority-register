package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.RegulatoryFunctionPage;

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
	
	public RegulatoryFunctionPage clickContinue() {
		continueBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
	
	public RegulatoryFunctionPage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
}
