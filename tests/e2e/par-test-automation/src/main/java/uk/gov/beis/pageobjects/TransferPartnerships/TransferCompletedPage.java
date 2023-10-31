package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.AuthorityPageObjects.AuthoritiesSearchPage;

public class TransferCompletedPage extends BasePageObject {
	
	@FindBy(xpath = "//a[contains(text(), 'Done')]")
	private WebElement doneBtn;
	
	public TransferCompletedPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AuthoritiesSearchPage selectDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, AuthoritiesSearchPage.class);
	}
}
