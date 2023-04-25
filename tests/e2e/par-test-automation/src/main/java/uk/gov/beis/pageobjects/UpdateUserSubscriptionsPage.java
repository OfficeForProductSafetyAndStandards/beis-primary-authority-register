package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UpdateUserSubscriptionsPage extends BasePageObject {
	public UpdateUserSubscriptionsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-subscriptions-par-news")
	private WebElement parNewsCheckbox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;

	public void selectPARNewsSubscription() {

		if (!parNewsCheckbox.isSelected())
			parNewsCheckbox.click();
	}

	public void selectPARNewsUnsubscription() {
		if (parNewsCheckbox.isSelected())
			parNewsCheckbox.click();
	}

	public UpdateUserConfirmationPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UpdateUserConfirmationPage.class);
	}

	public DashboardPage selectCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
