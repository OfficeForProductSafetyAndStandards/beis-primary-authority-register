package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class RemovePage extends BasePageObject {
	
	@FindBy(id = "edit-remove-reason")
	private WebElement removeReasonTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement nextBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public RemovePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterRemoveReason(String reason) {
		removeReasonTextArea.clear();
		removeReasonTextArea.sendKeys(reason);
	}
	
	public void selectRemoveButton() {
		nextBtn.click();
	}
	
	public void clickRemoveButton() {
		saveBtn.click();
	}
}
