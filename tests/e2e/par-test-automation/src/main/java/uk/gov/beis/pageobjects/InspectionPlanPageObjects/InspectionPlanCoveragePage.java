package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationSummaryPage;
import uk.gov.beis.utility.DataStore;

public class InspectionPlanCoveragePage extends BasePageObject {
	
	@FindBy(id = "edit-covered-by-inspection-1")
	private WebElement yesRadial;
	
	@FindBy(id = "edit-covered-by-inspection-0")
	private WebElement noRadial;
	
	@FindBy(xpath = "//input[@id='edit-covered-by-inspection-1']/following-sibling::label")
	private WebElement yesRadialLabel;
	
	@FindBy(xpath = "//input[@id='edit-covered-by-inspection-0']/following-sibling::label")
	private WebElement noRadialLabel;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public InspectionPlanCoveragePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectYesRadial() {
		yesRadial.click();
		
		DataStore.saveValue(UsableValues.COVERED_BY_INSPECTION_PLAN, yesRadialLabel.getText());
	}
	
	public void selectNoRadial() {
		noRadial.click();
		
		DataStore.saveValue(UsableValues.COVERED_BY_INSPECTION_PLAN, noRadialLabel.getText());
	}
	
	public MemberOrganisationSummaryPage selectContinueForMember() {
		continueBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
	
	public MemberOrganisationSummaryPage selectSaveForMember() {
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
}
