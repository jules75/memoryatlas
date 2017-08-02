
let Inline = Quill.import('blots/inline');

class DateBlot extends Inline {

  static create(yyyymmdd) {
    let node = super.create();
    node.setAttribute('data-tagtype', 'date');
    node.setAttribute('data-yyyymmdd', yyyymmdd);
    return node;
  }

  static formats(node) {
    return node.getAttribute('data-yyyymmdd');
  }

  static popupEditor(onOk) {

    createOverlay();

    var div = $(`
      <div id="calendarPicker" class="modal">
      <form>
      <div>
      <label>Year</label>
      <input id="year" type="text" autofocus="autofocus" autocomplete="off" />
      </div>
      <div>
      <label>Month</label>
      <select id="month">
      <option value="00">UNKNOWN</option>
      <option value="01">Jan</option>
      <option value="02">Feb</option>
      <option value="03">Mar</option>
      <option value="04">Apr</option>
      <option value="05">May</option>
      <option value="06">Jun</option>
      <option value="07">Jul</option>
      <option value="08">Aug</option>
      <option value="09">Sep</option>
      <option value="10">Oct</option>
      <option value="11">Nov</option>
      <option value="12">Dec</option>
      </select>
      </div>
      <div>
      <label>Day</label>
      <select id="day">
      <option value="00">UNKNOWN</option>
      <option value="01">01</option>
      <option value="02">02</option>
      <option value="03">03</option>
      <option value="04">04</option>
      <option value="05">05</option>
      <option value="06">06</option>
      <option value="07">07</option>
      <option value="08">08</option>
      <option value="09">09</option>
      <option value="10">10</option>
      <option value="11">11</option>
      <option value="12">12</option>
      <option value="13">13</option>
      <option value="14">14</option>
      <option value="15">15</option>
      <option value="16">16</option>
      <option value="17">17</option>
      <option value="18">18</option>
      <option value="19">19</option>
      <option value="10">20</option>
      <option value="21">21</option>
      <option value="22">22</option>
      <option value="23">23</option>
      <option value="24">24</option>
      <option value="25">25</option>
      <option value="26">26</option>
      <option value="27">27</option>
      <option value="28">28</option>
      <option value="29">29</option>
      <option value="30">30</option>
      <option value="31">31</option>
      </select>
      </div>
      </form>
      <button id="ok">Ok</button>
      <button id="cancel">Cancel</button>
      </div>
      `);

    $('body').append(div);

    let result = "00000000";    

    // close on any button
    $('#calendarPicker button').click(function (e) {
      $('#calendarPicker').remove();
      destroyOverlay();
    });

    // handle OK button
    $('#calendarPicker #ok').click(function (e) {
      onOk(result);
    });

    function processDate() {

      // if month is UNKNOWN, change day to UNKNOWN too
      if ($("#month").val() == "00") {
        $("#day").val("00");
      }

      // only show OK button when date is valid
      if (isValidYear($("#year").val())) {
        $("#ok").removeAttr("disabled");
      }
      else {
        $("#ok").attr("disabled", "disabled");
      }

      if (isValidYear($("#year").val())) {
        result = $("#year").val() + $("#month").val() + $("#day").val();
      }
      else {
        result = "00000000";
      }
    }

    function isValidYear(str) {

      // check for 4 digits
      if (str.search(/^\d{4}$/) != 0) {
        return false;
      }

      var year = Number(str);
      return (year > 1800 && year < 2100);
    }

    processDate();
    $("#year").on("input", processDate);
    $("#month, #day").change(processDate);
  }

  static popupShow(yyyymmdd) {

    function friendlyYYYYMMDD(yyyymmdd) {
      var months = ["??", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
      var [s, y, m, d] = yyyymmdd.match(/^(\d{4})(\d{2})(\d{2})$/);

      let month = months[Number(m)];

      if (d != "00") {
        return d + " " + month + " " + y;
      }

      if (m != "00") {
        return month + " " + y;
      }

      return y;
    }
    alert(friendlyYYYYMMDD(yyyymmdd));
  }

}

DateBlot.blotName = 'date';
DateBlot.tagName = 'span';
