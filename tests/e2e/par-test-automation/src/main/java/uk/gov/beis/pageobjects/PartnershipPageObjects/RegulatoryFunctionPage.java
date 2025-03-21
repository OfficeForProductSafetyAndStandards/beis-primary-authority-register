package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class RegulatoryFunctionPage extends BasePageObject {

	@FindBy(id = "edit-partnership-cover-default")
	private WebElement normalOrSequencedRadial;
	
	@FindBy(id = "edit-partnership-cover-bespoke")
	private WebElement bespokeRadial;
	
	@FindBy(xpath = "//input[@type='checkbox']")
	private WebElement bespokeCheckbox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String regFunction = "//div/label[contains(text(),'?')]/preceding-sibling::input";
	
	public RegulatoryFunctionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectNormalOrSequencedFunctions() {
		normalOrSequencedRadial.click();
	}
	
	public void deselectBespokeFunctions() {
		bespokeRadial.click();
		
		if(bespokeRadial.isSelected()) {
			if(bespokeCheckbox.isSelected()) {
				bespokeCheckbox.click();
			}
		}
	}
	
	public void selectBespokeFunctions() {
		bespokeRadial.click();
		
		if(bespokeRadial.isSelected()) {
			if(!bespokeCheckbox.isSelected()) {
				bespokeCheckbox.click();
			}
		}
	}
	
	public void selectRegFunction(String reg) {
		List<WebElement> boxes = driver.findElements(By.xpath("//div/label/preceding-sibling::input"));
		
		// clear up boxes first
		for (WebElement bx : boxes) {
			if (bx.isSelected()){
				bx.click();
			}
		}
		
		driver.findElement(By.xpath(regFunction.replace("?", reg))).click();
	}
	
	public void updateRegFunction() {
		if(normalOrSequencedRadial.isSelected()) {
			bespokeRadial.click();
			
			if(!bespokeCheckbox.isSelected()) {
				bespokeCheckbox.click();
			}
		}
		else if(bespokeRadial.isSelected()) {
			normalOrSequencedRadial.click();
		}
		
		//DataStore.saveValue(UsableValues.PARTNERSHIP_REGFUNC, "Cookie control"); // Would be better to use Bespoke and Normal or Sequenced as the value.
		DataStore.saveValue(UsableValues.PARTNERSHIP_REGFUNC, "Alphabet learning");
	}
	
	public void selectContinueButton() {
		continueBtn.click();
	}
	
	public void selectSaveButton() {
		saveBtn.click();
	}
}
