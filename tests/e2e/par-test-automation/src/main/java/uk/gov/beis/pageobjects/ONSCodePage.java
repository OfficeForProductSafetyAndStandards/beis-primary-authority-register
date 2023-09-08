package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ONSCodePage extends BasePageObject {

	public ONSCodePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-ons-code")
	private WebElement onsCode;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	public RegulatoryFunctionPage enterONSCode(String name) {
		onsCode.clear();
		onsCode.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
}
