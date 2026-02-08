using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.IO;

namespace win2kai
{
    public partial class Form1 : Form
    {
        private const string API_URL_MODELS = "http://192.168.100.1:8080/api/models";
        private const string API_URL_CHAT = "http://192.168.100.1:8080/api/chat";

        public Form1()
        {
            InitializeComponent();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            AppendSystemMessage("Connecting to API...");
            LoadModels();
        }

        private void LoadModels()
        {
            try
            {
                string rawData = HttpGet(API_URL_MODELS);
                string[] lines = rawData.Split(new char[] { '\n', '\r' }, StringSplitOptions.RemoveEmptyEntries);

                cmbModels.Items.Clear();

                foreach (string line in lines)
                {
                    string[] parts = line.Split('|');
                    if (parts.Length >= 2)
                    {
                        string id = parts[0];
                        string name = parts[1];
                        cmbModels.Items.Add(new ModelItem(name, id));
                    }
                }

                if (cmbModels.Items.Count > 0)
                {
                    cmbModels.SelectedIndex = 0;
                    AppendSystemMessage(cmbModels.Items.Count + " models loaded.");
                    txtInput.Focus();
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
                AppendSystemMessage("Cannot reach server.");
            }
        }

        private void btnSend_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrEmpty(txtInput.Text))
            {
                MessageBox.Show("Please enter a prompt.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }
            if (cmbModels.SelectedItem == null)
            {
                MessageBox.Show("Please select a model.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            string prompt = txtInput.Text;
            ModelItem selectedItem = (ModelItem)cmbModels.SelectedItem;
            string backInTime = chkRetro.Checked ? "1" : "0";

            rtbChat.SelectionStart = rtbChat.TextLength;
            rtbChat.SelectionLength = 0;
            rtbChat.SelectionColor = Color.Blue;
            rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Bold);
            rtbChat.AppendText("You");

            if (chkRetro.Checked)
            {
                rtbChat.SelectionColor = Color.Gray;
                rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Italic);
                rtbChat.AppendText(" (back in time modus)");
            }

            rtbChat.SelectionColor = Color.Blue;
            rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Bold);
            rtbChat.AppendText(": ");

            rtbChat.SelectionColor = Color.Black;
            rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Regular);
            rtbChat.AppendText(prompt + "\r\n\r\n");

            txtInput.Clear();
            btnSend.Enabled = false;
            this.Cursor = Cursors.WaitCursor;

            try
            {
                string postData = string.Format("prompt={0}&model={1}&back_in_time={2}",
                    Uri.EscapeDataString(prompt),
                    Uri.EscapeDataString(selectedItem.Id),
                    backInTime);

                string response = HttpPost(API_URL_CHAT, postData);
                AppendChatMessage("AI (" + selectedItem.Name + ")", response, Color.Black);
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
            }
            finally
            {
                this.Cursor = Cursors.Default;
                btnSend.Enabled = true;
                txtInput.Focus();
                rtbChat.ScrollToCaret();
            }
        }

        private void AppendChatMessage(string user, string text, Color color)
        {
            rtbChat.SelectionStart = rtbChat.TextLength;
            rtbChat.SelectionLength = 0;
            rtbChat.SelectionColor = color;
            rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Bold);
            rtbChat.AppendText(user + ": ");

            rtbChat.SelectionColor = Color.Black;
            rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Regular);
            rtbChat.AppendText(text + "\r\n\r\n");
        }

        private void AppendSystemMessage(string text)
        {
            rtbChat.SelectionStart = rtbChat.TextLength;
            rtbChat.SelectionLength = 0;
            rtbChat.SelectionColor = Color.Gray;
            rtbChat.SelectionFont = new Font(rtbChat.Font, FontStyle.Italic);
            rtbChat.AppendText("System: " + text + "\r\n\r\n");
        }

        private string HttpGet(string url)
        {
            WebRequest request = WebRequest.Create(url);
            using (WebResponse response = request.GetResponse())
            using (StreamReader reader = new StreamReader(response.GetResponseStream()))
            {
                return reader.ReadToEnd();
            }
        }

        private string HttpPost(string url, string postData)
        {
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";
            request.Accept = "text/plain";

            byte[] byteArray = Encoding.UTF8.GetBytes(postData);
            request.ContentLength = byteArray.Length;

            using (Stream dataStream = request.GetRequestStream())
            {
                dataStream.Write(byteArray, 0, byteArray.Length);
            }

            using (WebResponse response = request.GetResponse())
            using (StreamReader reader = new StreamReader(response.GetResponseStream()))
            {
                return reader.ReadToEnd();
            }
        }

        private class ModelItem
        {
            private string _name;
            private string _id;

            public string Name
            {
                get { return _name; }
                set { _name = value; }
            }

            public string Id
            {
                get { return _id; }
                set { _id = value; }
            }

            public ModelItem(string name, string id)
            {
                _name = name;
                _id = id;
            }

            public override string ToString()
            {
                return _name;
            }
        }

        private void txtInput_TextChanged(object sender, EventArgs e)
        {

        }
    }
}

