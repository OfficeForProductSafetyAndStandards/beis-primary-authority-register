package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class BlockPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement blockBtn;
	
	public BlockPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickBlockButton() {
		blockBtn.click();
	}
}
