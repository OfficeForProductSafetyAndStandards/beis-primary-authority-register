package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class MembershipCeasedPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement ceaseBtn;
	
	public MembershipCeasedPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public MemberListPage goToMembersListPage() {
		ceaseBtn.click();
		return PageFactory.initElements(driver, MemberListPage.class);
	}
}
