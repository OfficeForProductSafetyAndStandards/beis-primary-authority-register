package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnforcementCompletionPage extends BasePageObject{

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public EnforcementCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public EnforcementSearchPage complete() {
		doneBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
}
