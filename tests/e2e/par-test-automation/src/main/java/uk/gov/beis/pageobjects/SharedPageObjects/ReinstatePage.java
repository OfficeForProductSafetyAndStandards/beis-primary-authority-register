package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ReinstatePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement reinstateBtn;
	
	public ReinstatePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickReinstateButton() {
		reinstateBtn.click();
	}
}
