package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ManageColleaguesPage extends BasePageObject {
	public ManageColleaguesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(linkText = "Add a person")
	private WebElement addPersonBtn;
	
	@FindBy(id = "edit-name-email-search")
	private WebElement nameEmailTextField;
	
	@FindBy(id = "edit-authority-organisation-filter")
	private WebElement authorityOrganisationTextField;
	
	@FindBy(id = "edit-submit-par-people")
	private WebElement submitBtn;
	
	@FindBy(linkText = "back to dashboard")
	private WebElement dashboardBtn;
	
	public AddPersonPage selectAddPerson() {
		addPersonBtn.click();
		return PageFactory.initElements(driver, AddPersonPage.class);
	}
	
	public void enterNameOrEmail(String searchText) {
		nameEmailTextField.sendKeys(searchText);
	}
	
	public void enterAuthorityOrOrganisation(String searchText) {
		authorityOrganisationTextField.sendKeys(searchText);
	}
	
	public void clickSubmit() {
		submitBtn.click();
	}
	
	public DashboardPage clickDashboadButton() {
		dashboardBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		dashboardBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
