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
	
	public RegulatoryFunctionPage enterONSCode(String name) {
		onsCode.clear();
		onsCode.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
	
	public RegulatoryFunctionPage editONSCode(String name) {
		onsCode.clear();
		onsCode.sendKeys(name);
		
		saveBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
}
