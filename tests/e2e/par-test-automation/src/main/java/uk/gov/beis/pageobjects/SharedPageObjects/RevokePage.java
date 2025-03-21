package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class RevokePage extends BasePageObject {
	
	@FindBy(id = "edit-revocation-reason")
	private WebElement reasonTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement nextBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public RevokePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterReasonForRevocation(String reason) {
		reasonTextArea.clear();
		reasonTextArea.sendKeys(reason);
	}
	
	public void clickRevokeButton() {
		nextBtn.click();
	}
	
	public void selectRevokeButton(){
		saveBtn.click();
	}
}
