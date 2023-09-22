package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class InspectionPlanCoveragePage extends BasePageObject {
	
	@FindBy(id = "edit-covered-by-inspection-1")
	private WebElement yesRadial;
	
	@FindBy(id = "edit-covered-by-inspection-0")
	private WebElement noRadial;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public InspectionPlanCoveragePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectYesRadial() {
		yesRadial.click();
	}
	
	public void selectNoRadial() {
		noRadial.click();
	}
	
	public MemberOrganisationSummaryPage selectContinueForMember() {
		continueBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
}
