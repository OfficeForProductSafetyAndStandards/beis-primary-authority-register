package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class CompletionPage extends BasePageObject {

	@FindBy(id = "edit-done")
	private WebElement doneBtn;
	
	@FindBy(linkText = "Done")
	private WebElement doneLink;
	
	public CompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickDoneForPartnership() {
		doneBtn.click();
	}
	
	public void clickDoneForInvitation() {
		doneLink.click();
	}
}
