package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class NewsLetterSubscriptionReviewPage extends BasePageObject {
	public NewsLetterSubscriptionReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(id = "edit-save")
	private WebElement updateListBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public NewsLetterSubscriptionPage clickUpdateListButton() {
		updateListBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}
	
	public NewsLetterSubscriptionPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}
}
